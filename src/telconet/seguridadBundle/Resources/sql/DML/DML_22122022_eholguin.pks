/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 22-12-2022
 * Se crean las sentencias DML para creación de de perfiles asignados a nivel de tabla SEGU_ASIGNACION.
 */
 
-- CONSULTAR CASOS --
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            11602,
            562,
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
        
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9135,
            562,
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
        
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9134,
            562,
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        ); 
                
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9131,
            562,
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        ); 
        
        
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9289,
            562,
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );  

-- VER RESUMEN CLIENTE -- 

INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9127,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        ); 
        
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9128,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );

INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10226,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9125,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10823,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            11602,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10223,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9146,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9149,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9329,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9124,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );

INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9130,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );

INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9147,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9148,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9158,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9335,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10283,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9135,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10224,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9133,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            6546,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9123,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9145,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10082,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10225,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9150,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9328,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9603,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9129,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            11564,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9151,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            6244,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9131,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9152,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9157,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10783,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            11543,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9289,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9126,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9203,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9297,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10843,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9134,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9153,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9333,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9334,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8897),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
-- VER DIAGNOSTICO OSS --

INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9127,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        ); 
        
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9128,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );

INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10226,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9125,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10823,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            11602,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10223,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9146,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9149,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9329,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9124,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9130,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9147,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9148,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9158,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9335,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10283,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9135,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10224,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9133,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            6546,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9123,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9145,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10082,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10225,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9150,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9328,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9603,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9129,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            11564,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9151,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            6244,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9131,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9152,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9157,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10783,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            11543,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9289,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9126,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9203,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9297,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            10843,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9134,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9153,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9333,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
INSERT INTO DB_SEGURIDAD.SEGU_ASIGNACION (
            PERFIL_ID,
            RELACION_SISTEMA_ID,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION
        ) VALUES (
            9334,
            (SELECT ID_RELACION_SISTEMA FROM DB_SEGURIDAD.SEGU_RELACION_SISTEMA WHERE MODULO_ID = 151 AND ACCION_ID = 8898),
            'telcosReg',
            SYSDATE,
            '127.0.0.1'
        );
 COMMIT;        
  /      
                       
                        
                                                               
                              
