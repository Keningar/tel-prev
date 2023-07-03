--=======================================================================
-- Ingreso de parametros para mostrar las características del producto
-- en el grid del modulo técnico
-- Ingreso de parametros para los estados de los servicios en el grid técnico
-- Ingreso de parametros para los tipos de servicios en el grid técnico
--=======================================================================

-- INGRESO LAS CABECERAS DE PARAMETROS PARA LA CARACTERISTICAS DEL PRODUCTO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
( 
        ID_PARAMETRO, 
        NOMBRE_PARAMETRO, 
        DESCRIPCION, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
        'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO', 
        'Nombres de las características del producto que se van a mostrar en el grid técnico', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO LOS DETALLES DE LOS ID DE LOS PRODUCTOS Y CARACTERISTICAS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'979', 
    	'740', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'780', 
    	'740', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'780', 
    	'787', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'781', 
    	'740', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'781', 
    	'787', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'781', 
    	'806', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'238', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'238', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1264', 
    	'1398', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1264', 
    	'1401', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1264', 
    	'1022', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'281', 
    	'756', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'228', 
    	'693', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'242', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'242', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'901', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'901', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1254', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1254', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1246', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1246', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1155', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1155', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1241', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1241', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'243', 
    	'725', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'970', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'970', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'868', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'868', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'237', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'237', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1258', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1258', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'793', 
    	'740', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'793', 
    	'787', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'793', 
    	'806', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'289', 
    	'773', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'233', 
    	'698', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1269', 
    	'1417', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1269', 
    	'1418', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1071', 
    	'989', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1071', 
    	'935', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1068', 
    	'779', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1068', 
    	'892', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1067', 
    	'893', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1067', 
    	'838', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1067', 
    	'894', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1070', 
    	'887', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1070', 
    	'777', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1070', 
    	'746', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1066', 
    	'736', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1066', 
    	'1020', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1066', 
    	'891', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1064', 
    	'761', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1064', 
    	'760', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1069', 
    	'866', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1069', 
    	'865', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1268', 
    	'1416', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1268', 
    	'865', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1074', 
    	'738', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1074', 
    	'837', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1244', 
    	'738', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1244', 
    	'1316', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1189', 
    	'1150', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1189', 
    	'1151', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1073', 
    	'833', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1073', 
    	'890', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'921', 
    	'735', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1236', 
    	'1253', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1255', 
    	'1', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES 
( 
    	DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
    	( 
       	    SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'NOMBRES_CARACTERISTICAS_PRODUCTO_GRID_TECNICO' 
            AND ESTADO = 'Activo' 
    	), 
    	'LISTA VALORES', 
    	'1255', 
    	'2', 
    	'Activo', 
    	'facaicedo', 
    	SYSDATE, 
    	'127.0.0.1', 
    	( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
    	) 
);
-- INGRESO LAS CABECERAS DE PARAMETROS PARA LA ESTADOS DE LOS SERVICIOS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
( 
        ID_PARAMETRO, 
        NOMBRE_PARAMETRO, 
        DESCRIPCION, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
        'ESTADOS_SERVICIOS_GRID_TECNICO', 
        'Listado de los nombres de estados de los servicios para el filtro del grid técnico', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO LOS DETALLES DE LOS ESTADOS DE LOS SERVICIOS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Todos', 
        'Todos', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Asignada', 
        'Asignada', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'EnVerificacion', 
        'EnVerificacion', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Pendiente', 
        'Pendiente', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'EnPruebas', 
        'EnPruebas', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Activo', 
        'Activo', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'In-Corte-SinEje', 
        'Inactivo Corte Sin Ejecucion', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'In-Corte', 
        'Inactivo Corte', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'In-Temp-SinEje', 
        'Inactivo Temporal Sin Ejecucion', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'In-Temp', 
        'Inactivo Temporal', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Cancel-SinEje', 
        'Cancelado Sin Ejecucion', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Cancel', 
        'Cancelado', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
-- INGRESO LAS CABECERAS DE PARAMETROS PARA LOS TIPOS DE SERVICIOS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
( 
        ID_PARAMETRO, 
        NOMBRE_PARAMETRO, 
        DESCRIPCION, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
        'TIPOS_SERVICIOS_GRID_TECNICO', 
        'Listado de los tipos de los servicios para el filtro del grid técnico', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO LOS DETALLES DE LOS TIPOS DE SERVICIOS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'TIPOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'N', 
        'Nueva', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'TIPOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'R', 
        'Reubicación', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'TIPOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'T', 
        'Traslado', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION, 
        EMPRESA_COD 
) 
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
        ( 
            SELECT ID_PARAMETRO 
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
            WHERE NOMBRE_PARAMETRO = 'TIPOS_SERVICIOS_GRID_TECNICO' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'C', 
        'Cambio Tipo Medio', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
COMMIT;
/
