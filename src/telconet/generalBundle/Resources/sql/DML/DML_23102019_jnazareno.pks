/*
Creación de estructura para obtener fotos obligatorias dependiendo la tarea
*/ 

----INICIO DE PARÁMETRO DE PORCENTAJE DE VALIDEZ DE FOTO

INSERT INTO db_general.admi_parametro_cab 
(
  ID_PARAMETRO,
  NOMBRE_PARAMETRO,
  DESCRIPCION,
  MODULO,
  PROCESO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD
)
VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'PORCENTAJE_MIN_FOTOS_OBLIGATORIAS',
    'PARÁMETRO DE PORCENTAJE DE VALIDEZ DE FOTO',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL
);

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
            nombre_parametro = 'PORCENTAJE_MIN_FOTOS_OBLIGATORIAS'
    ),
    'PARÁMETRO DE PORCENTAJE DE VALIDEZ DE FOTO',
    '70',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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

--FIN DE PARÁMETRO DE PORCENTAJE DE VALIDEZ DE FOTO

-- INICIO DE CREACION DE PARAMETRO DE LA RUTA DE IMAGENES IDEALES
INSERT INTO db_general.admi_parametro_cab 
(
  ID_PARAMETRO,
  NOMBRE_PARAMETRO,
  DESCRIPCION,
  MODULO,
  PROCESO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD
)
VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'RUTA_BASE_IMAGENES_IDEALES',
    'RETORNA LA RUTA DE IMAGENES IDEALES',
    'TECNICO',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL
);

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
                    nombre_parametro = 'RUTA_BASE_IMAGENES_IDEALES'
            ),
    'RUTA BASE DE IMAGENES IDEALES',
    'public/uploads/tareas/ideal/',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
--FIN DE CREACION DE PARAMETRO DE LA RUTA DE IMAGENES IDEALES

----INICIO DE PARÁMETRO DE CRONOLOGÍA DE FOTOS OBLIGATORIAS

INSERT INTO db_general.admi_parametro_cab 
(
  ID_PARAMETRO,
  NOMBRE_PARAMETRO,
  DESCRIPCION,
  MODULO,
  PROCESO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD
)
VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'CRONOLOGIA_FOTOS_OBLIGATORIAS',
    'PARÁMETRO DE CRONOLOGIA DE FOTOS OBLIGATORIAS',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL
);

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
            nombre_parametro = 'CRONOLOGIA_FOTOS_OBLIGATORIAS'
    ),
    'PARÁMETRO DE CRONOLOGIA DE FOTOS OBLIGATORIAS',
    'ANTES',
    'DESPUES',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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

--FIN DE PARÁMETRO DE CRONOLOGÍA DE FOTOS OBLIGATORIAS

----INICIO DE PARÁMETRO DE INTENTOS MÁXIMOS DE FOTOS OBLIGATORIAS

INSERT INTO db_general.admi_parametro_cab 
(
  ID_PARAMETRO,
  NOMBRE_PARAMETRO,
  DESCRIPCION,
  MODULO,
  PROCESO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD
)
VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'INTENTOS_MAX_FOTOS_OBLIGATORIAS',
    'PARÁMETRO DE INTENTOS MÁXIMOS DE FOTOS OBLIGATORIAS',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL
);

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
            nombre_parametro = 'INTENTOS_MAX_FOTOS_OBLIGATORIAS'
    ),
    'PARÁMETRO DE INTENTOS MÁXIMOS DE FOTOS OBLIGATORIAS',
    '100',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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

--FIN DE PARÁMETRO DE INTENTOS MÁXIMOS DE FOTOS OBLIGATORIAS

----INICIO DE PARÁMETRO DE VALORES DE EVALUACION

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
            nombre_parametro = 'VALORES_EVALUACION_TRABAJO'
    ),
    'Valores para la evaluación de trabajo de imágenes',
    'DAÑO',
    'DAÑO',
    '3',
    NULL,
    'Activo',
    'jnazareno',
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
-----------------------------------------------------------------------

--FIN DE PARÁMETRO DE VALORES DE EVALUACION

----INICIO DE PARÁMETROS PARA EL MOVIL

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
    'ESTADO PARA FOTOS ANTES Y DESPUES',
    'ESTADO_FOTO_OK',
    'OK',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
    'ESTADO PARA FOTOS ANTES Y DESPUES',
    'ESTADO_FOTO_MALO',
    'MALO',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
    'ESTADO PARA FOTOS ANTES Y DESPUES',
    'ESTADO_FOTO_DAÑO',
    'DAÑO',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
    'ESTADO PARA FOTOS ANTES Y DESPUES',
    'ESTADO_FOTO_PENDIENTE',
    'Pendiente',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
    'ESTADO PARA FOTOS ANTES Y DESPUES',
    'ESTADO_FOTO_AUDITADA',
    'Auditada',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
    'ESTADO PARA FOTOS ANTES Y DESPUES',
    'ESTADO_FOTO_EN_PROCESO',
    'En Proceso',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
    'CRONOLOGÍA PARA FOTOS ANTES Y DESPUES',
    'CRONOLOGIA_FOTO_ANTES',
    'ANTES',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
    'CRONOLOGÍA PARA FOTOS ANTES Y DESPUES',
    'CRONOLOGIA_FOTO_DESPUES',
    'DESPUES',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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
    'MÍNIMO DE ELEMENTOS A VALIDAR PARA FOTOS ANTES Y DESPUES',
    'MIN_ELEMENTOS_FOTOS_VALIDAR',
    '1',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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

-----------------------------------------------------------------------

----FIN DE PARÁMETROS PARA EL MOVIL

----INICIO DE PARÁMETRO DE TAREA QUE SE CREARÁ AUTOMATICAMENTE

INSERT INTO db_general.admi_parametro_cab 
(
  ID_PARAMETRO,
  NOMBRE_PARAMETRO,
  DESCRIPCION,
  MODULO,
  PROCESO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD
)
VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'TAREA_AUTOMATICA_FOTOS_OBLIGATORIAS',
    'PARÁMETRO DE TAREA QUE SE CREARÁ AUTOMATICAMENTE',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL
);

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
            nombre_parametro = 'TAREA_AUTOMATICA_FOTOS_OBLIGATORIAS'
    ),
    'PARÁMETRO DE TAREA QUE SE CREARÁ AUTOMATICAMENTE',
    '1346',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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

--FIN DE PARÁMETRO DE TAREA QUE SE CREARÁ AUTOMATICAMENTE

----INICIO DE PARÁMETRO DE ESTADO PARA TAREA QUE SE CREARÁ AUTOMATICAMENTE

INSERT INTO db_general.admi_parametro_cab 
(
  ID_PARAMETRO,
  NOMBRE_PARAMETRO,
  DESCRIPCION,
  MODULO,
  PROCESO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD
)
VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'ESTADO_CREACION_TAREA_FOTOS_OBLIGATORIAS',
    'PARÁMETRO DE ESTADO PARA TAREA QUE SE CREARÁ AUTOMATICAMENTE',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL
);

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
            nombre_parametro = 'ESTADO_CREACION_TAREA_FOTOS_OBLIGATORIAS'
    ),
    'PARÁMETRO DE ESTADO PARA TAREA QUE SE CREARÁ AUTOMATICAMENTE',
    'DAÑO',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
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

--FIN DE PARÁMETRO DE ESTADO PARA TAREA QUE SE CREARÁ AUTOMATICAMENTE

----INICIO FOTOS OBLIGATORIAS OPU

INSERT INTO db_general.admi_parametro_cab 
(
  ID_PARAMETRO,
  NOMBRE_PARAMETRO,
  DESCRIPCION,
  MODULO,
  PROCESO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  IP_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  IP_ULT_MOD
)
VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'FOTOS_OBLIGATORIAS',
    'PARAMETRO USADO PARA LA ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL
);

--PEDESTAL ALTA DENSIDAD UIO 18
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'PEDESTAL',
    'PEDESTAL ALTA DENSIDAD UIO',
    'PEDESTAL_ALTA_DENSIDAD_UIO_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '18',
    NULL,
    NULL,
    NULL
);

--PEDESTAL ALTA DENSIDAD UIO 10
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'PEDESTAL',
    'PEDESTAL ALTA DENSIDAD UIO',
    'PEDESTAL_ALTA_DENSIDAD_UIO_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '10',
    NULL,
    NULL,
    NULL
);

--PEDESTAL BAJA DENSIDAD UIO 18
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'PEDESTAL',
    'PEDESTAL BAJA DENSIDAD UIO',
    'PEDESTAL_BAJA_DENSIDAD_UIO_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '18',
    NULL,
    NULL,
    NULL
);

--PEDESTAL BAJA DENSIDAD UIO 10
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'PEDESTAL',
    'PEDESTAL BAJA DENSIDAD UIO',
    'PEDESTAL_BAJA_DENSIDAD_UIO_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '10',
    NULL,
    NULL,
    NULL
);

--PEDESTAL BAJA DENSIDAD GYE 18
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'PEDESTAL',
    'PEDESTAL BAJA DENSIDAD GYE',
    'PEDESTAL_BAJA_DENSIDAD_GYE_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '18',
    NULL,
    NULL,
    NULL
);

--PEDESTAL BAJA DENSIDAD GYE 10
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'PEDESTAL',
    'PEDESTAL BAJA DENSIDAD GYE',
    'PEDESTAL_BAJA_DENSIDAD_GYE_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '10',
    NULL,
    NULL,
    NULL
);

--CAJA DISTRIBUCION MODELO 1 GYE 18
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'CAJA',
    'CAJA DISTRIBUCION MODELO 1 GYE',
    'CAJA_DISTRIBUCION_MODELO_1_GYE_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '18',
    NULL,
    NULL,
    NULL
);

--CAJA DISTRIBUCION MODELO 1 GYE 10
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'CAJA',
    'CAJA DISTRIBUCION MODELO 1 GYE',
    'CAJA_DISTRIBUCION_MODELO_1_GYE_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '10',
    NULL,
    NULL,
    NULL
);

--CAJA DISTRIBUCION MODELO 1 UIO 18
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'CAJA',
    'CAJA DISTRIBUCION MODELO 1 UIO',
    'CAJA_DISTRIBUCION_MODELO_1_UIO_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '18',
    NULL,
    NULL,
    NULL
);

--CAJA DISTRIBUCION MODELO 1 UIO 10
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'CAJA',
    'CAJA DISTRIBUCION MODELO 1 UIO',
    'CAJA_DISTRIBUCION_MODELO_1_UIO_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '10',
    NULL,
    NULL,
    NULL
);

--CAJA DISTRIBUCION MODELO 2 GYE 18
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'CAJA',
    'CAJA DISTRIBUCION MODELO 2 GYE',
    'CAJA_DISTRIBUCION_MODELO_2_GYE_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '18',
    NULL,
    NULL,
    NULL
);

--CAJA DISTRIBUCION MODELO 2 GYE 10
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'CAJA',
    'CAJA DISTRIBUCION MODELO 2 GYE',
    'CAJA_DISTRIBUCION_MODELO_2_GYE_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '10',
    NULL,
    NULL,
    NULL
);

--CAJA DISTRIBUCION MODELO 2 UIO 18
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'CAJA',
    'CAJA DISTRIBUCION MODELO 2 UIO',
    'CAJA_DISTRIBUCION_MODELO_2_UIO_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '18',
    NULL,
    NULL,
    NULL
);

--CAJA DISTRIBUCION MODELO 2 UIO 10
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'CAJA',
    'CAJA DISTRIBUCION MODELO 2 UIO',
    'CAJA_DISTRIBUCION_MODELO_2_UIO_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '10',
    NULL,
    NULL,
    NULL
);

--MINIPOSTE GYE 18
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'MINIPOSTE',
    'MINIPOSTE GYE',
    'MINIPOSTE_GYE_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '18',
    NULL,
    NULL,
    NULL
);

--MINIPOSTE GYE 10
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
            nombre_parametro = 'FOTOS_OBLIGATORIAS'
    ),
    'ESTRUCTURA DE FOTOS OBLIGATORIAS',
    'MINIPOSTE',
    'MINIPOSTE GYE',
    'MINIPOSTE_GYE_IDEAL.jpg',
    '849',
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    '128',
    '10',
    NULL,
    NULL,
    NULL
);

--------------------------------------------------------------------------------

--FIN FOTOS OBLIGATORIAS OPU

COMMIT;
/
