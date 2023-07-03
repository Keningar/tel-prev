
/**
 *
 * Se realiza la creación de parámetros para el proyecto 'TN: INT: Comercial: Nuevo: Interacion Telcos portal SD contrato digial'
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2021
 */
--Ingresamos la cabecera de parámetros
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS_SECURITY_DATA',
    'PARAMETROS AUXILIARES QUE INTERACTUAN CON SECURITY DATA',
    'COMERCIAL',
    'CONTRATO_DIGITAL_SD',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1'
);
--Ingresamos el detalle de parámetros
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_SECURITY_DATA'
            AND ESTADO = 'Activo'
    ),
    'LISTA_USUARIO_COBRANZA',
    'mfranco',
    'R1',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10,
    'Valor1: usuario configurado para la asignación de tarea, Valor2: filtrar por región'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_SECURITY_DATA'
            AND ESTADO = 'Activo'
    ),
    'LISTA_USUARIO_COBRANZA',
    'imolina',
    'R2',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10,
    'Valor1: usuario configurado para la asignación de tarea, Valor2: filtrar por región'
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
    EMPRESA_COD,
    OBSERVACION
)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_SECURITY_DATA'
            AND ESTADO = 'Activo'
    ),
    'TAREA_PROCESO',
    'TAREAS DE COBRANZAS - GESTIÓN COMERCIAL',
    'APROBACION DE CONTRATO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10,
    'Valor1: nombre del proceso, Valor2: nombre de la tarea'
);
--Ingresamos las características
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'REFERENCIA_SOLICITUD_SD',
    'C',
    'Activo',
    SYSDATE,
    'kbaque',
    'COMERCIAL'
);

--Ingresamos nuevos tipos de documentos para el TelcoS+
INSERT INTO DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL (
    ID_TIPO_DOCUMENTO,
    CODIGO_TIPO_DOCUMENTO,
    DESCRIPCION_TIPO_DOCUMENTO,
    ESTADO,
    USR_CREACION,
    IP_CREACION,
    FE_CREACION,
    VISIBLE,
    PERSONA,
    ELEMENTO
) VALUES (
    DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
    'ORSER',
    'ORDEN DE SERVICIO',
    'Activo',
    'kbaque',
    '127.0.0.1',
    SYSDATE,
    'S',
    'N',
    'N'
);
INSERT INTO DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL (
    ID_TIPO_DOCUMENTO,
    CODIGO_TIPO_DOCUMENTO,
    DESCRIPCION_TIPO_DOCUMENTO,
    ESTADO,
    USR_CREACION,
    IP_CREACION,
    FE_CREACION,
    VISIBLE,
    PERSONA,
    ELEMENTO
) VALUES (
    DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
    'AD',
    'ADEMDUM',
    'Activo',
    'kbaque',
    '127.0.0.1',
    SYSDATE,
    'S',
    'N',
    'N'
);
INSERT INTO DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL (
    ID_TIPO_DOCUMENTO,
    CODIGO_TIPO_DOCUMENTO,
    DESCRIPCION_TIPO_DOCUMENTO,
    ESTADO,
    USR_CREACION,
    IP_CREACION,
    FE_CREACION,
    VISIBLE,
    PERSONA,
    ELEMENTO
) VALUES (
    DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
    'ESC',
    'ESCRITURA',
    'Activo',
    'kbaque',
    '127.0.0.1',
    SYSDATE,
    'S',
    'N',
    'N'
);
INSERT INTO DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL (
    ID_TIPO_DOCUMENTO,
    CODIGO_TIPO_DOCUMENTO,
    DESCRIPCION_TIPO_DOCUMENTO,
    ESTADO,
    USR_CREACION,
    IP_CREACION,
    FE_CREACION,
    VISIBLE,
    PERSONA,
    ELEMENTO
) VALUES (
    DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
    'CACOM',
    'CARTA DE COMPROMISO',
    'Activo',
    'kbaque',
    '127.0.0.1',
    SYSDATE,
    'S',
    'N',
    'N'
);
INSERT INTO DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL (
    ID_TIPO_DOCUMENTO,
    CODIGO_TIPO_DOCUMENTO,
    DESCRIPCION_TIPO_DOCUMENTO,
    ESTADO,
    USR_CREACION,
    IP_CREACION,
    FE_CREACION,
    VISIBLE,
    PERSONA,
    ELEMENTO
) VALUES (
    DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
    'COCON',
    'CÓDIGO DE CONDUCTA',
    'Activo',
    'kbaque',
    '127.0.0.1',
    SYSDATE,
    'S',
    'N',
    'N'
);
INSERT INTO DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL (
    ID_TIPO_DOCUMENTO,
    CODIGO_TIPO_DOCUMENTO,
    DESCRIPCION_TIPO_DOCUMENTO,
    ESTADO,
    USR_CREACION,
    IP_CREACION,
    FE_CREACION,
    VISIBLE,
    PERSONA,
    ELEMENTO
) VALUES (
    DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
    'CEDRE',
    'CÉDULA REPRESENTANTE',
    'Activo',
    'kbaque',
    '127.0.0.1',
    SYSDATE,
    'S',
    'N',
    'N'
);
--Ingresamos nuevos tipos de documentos para el Gestor Documental
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO (
    ID_TIPO_DOCUMENTO,
    DESCRIPCION,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CODIGO,
    ESTADO
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCUMENTO.NEXTVAL,
    'Escritura',
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    TO_CHAR((
        SELECT
            MAX(TO_NUMBER(CODIGO)) + 1 AS CODIGO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
    )),
    'Activo'
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO (
    ID_TIPO_DOCUMENTO,
    DESCRIPCION,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CODIGO,
    ESTADO
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCUMENTO.NEXTVAL,
    'Ruc',
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    TO_CHAR((
        SELECT
            MAX(TO_NUMBER(CODIGO)) + 1 AS CODIGO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
    )),
    'Activo'
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO (
    ID_TIPO_DOCUMENTO,
    DESCRIPCION,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CODIGO,
    ESTADO
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCUMENTO.NEXTVAL,
    'Carta De Compromiso',
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    TO_CHAR((
        SELECT
            MAX(TO_NUMBER(CODIGO)) + 1 AS CODIGO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
    )),
    'Activo'
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO (
    ID_TIPO_DOCUMENTO,
    DESCRIPCION,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CODIGO,
    ESTADO
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCUMENTO.NEXTVAL,
    'Código De Conducta',
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    TO_CHAR((
        SELECT
            MAX(TO_NUMBER(CODIGO)) + 1 AS CODIGO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
    )),
    'Activo'
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO (
    ID_TIPO_DOCUMENTO,
    DESCRIPCION,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CODIGO,
    ESTADO
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCUMENTO.NEXTVAL,
    'Cédula Representante',
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    TO_CHAR((
        SELECT
            MAX(TO_NUMBER(CODIGO)) + 1 AS CODIGO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
    )),
    'Activo'
);
--Ingresamos los filtros de búsqueda para los tipos de documentos
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA (
    ID_TIPO_DOCU_ETIQUETA,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    MAX_LENGTH,
    MIN_LENGTH,
    OBLIGATORIO,
    ORDEN,
    REGEX,
    TIPO_DOCUMENTO_ID,
    LABEL_KEY,
    TIPO_DATO,
    FRACTIONS
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCU_ETIQUETA.NEXTVAL,
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    30,
    1,
    'N',
    1,
    '^([0-9|a-zA-Z]+[-| ])*[0-9|a-zA-Z]+$',
    (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION = 'Escritura'
    ),
    'contractNumber',
    'Text',
    0
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA (
    ID_TIPO_DOCU_ETIQUETA,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    MAX_LENGTH,
    MIN_LENGTH,
    OBLIGATORIO,
    ORDEN,
    REGEX,
    TIPO_DOCUMENTO_ID,
    LABEL_KEY,
    TIPO_DATO,
    FRACTIONS
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCU_ETIQUETA.NEXTVAL,
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    30,
    1,
    'N',
    1,
    '^([0-9|a-zA-Z]+[-| ])*[0-9|a-zA-Z]+$',
    (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION = 'Ruc'
    ),
    'contractNumber',
    'Text',
    0
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA (
    ID_TIPO_DOCU_ETIQUETA,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    MAX_LENGTH,
    MIN_LENGTH,
    OBLIGATORIO,
    ORDEN,
    REGEX,
    TIPO_DOCUMENTO_ID,
    LABEL_KEY,
    TIPO_DATO,
    FRACTIONS
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCU_ETIQUETA.NEXTVAL,
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    30,
    1,
    'N',
    1,
    '^([0-9|a-zA-Z]+[-| ])*[0-9|a-zA-Z]+$',
    (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION = 'Carta De Compromiso'
    ),
    'contractNumber',
    'Text',
    0
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA (
    ID_TIPO_DOCU_ETIQUETA,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    MAX_LENGTH,
    MIN_LENGTH,
    OBLIGATORIO,
    ORDEN,
    REGEX,
    TIPO_DOCUMENTO_ID,
    LABEL_KEY,
    TIPO_DATO,
    FRACTIONS
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCU_ETIQUETA.NEXTVAL,
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    30,
    1,
    'N',
    1,
    '^([0-9|a-zA-Z]+[-| ])*[0-9|a-zA-Z]+$',
    (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION = 'Código De Conducta'
    ),
    'contractNumber',
    'Text',
    0
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA (
    ID_TIPO_DOCU_ETIQUETA,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    MAX_LENGTH,
    MIN_LENGTH,
    OBLIGATORIO,
    ORDEN,
    REGEX,
    TIPO_DOCUMENTO_ID,
    LABEL_KEY,
    TIPO_DATO,
    FRACTIONS
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCU_ETIQUETA.NEXTVAL,
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    30,
    1,
    'N',
    3,
    '^([0-9|a-zA-Z]+[-| ])*[0-9|a-zA-Z]+$',
    (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION = 'Ademdum'
    ),
    'contractNumber',
    'Text',
    0
);

INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA (
    ID_TIPO_DOCU_ETIQUETA,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    MAX_LENGTH,
    MIN_LENGTH,
    OBLIGATORIO,
    ORDEN,
    REGEX,
    TIPO_DOCUMENTO_ID,
    LABEL_KEY,
    TIPO_DATO,
    FRACTIONS
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCU_ETIQUETA.NEXTVAL,
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    30,
    1,
    'N',
    1,
    '^([0-9|a-zA-Z]+[-| ])*[0-9|a-zA-Z]+$',
    (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION = 'Cédula Representante'
    ),
    'contractNumber',
    'Text',
    0
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA (
    ID_TIPO_DOCU_ETIQUETA,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    MAX_LENGTH,
    MIN_LENGTH,
    OBLIGATORIO,
    ORDEN,
    REGEX,
    TIPO_DOCUMENTO_ID,
    LABEL_KEY,
    TIPO_DATO,
    FRACTIONS
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCU_ETIQUETA.NEXTVAL,
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    30,
    1,
    'N',
    3,
    '^([0-9|a-zA-Z]+[-| ])*[0-9|a-zA-Z]+$',
    (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION = 'Nombramiento'
    ),
    'contractNumber',
    'Text',
    0
);
INSERT INTO DB_DOCUMENTAL.ADMI_TIPO_DOCU_ETIQUETA (
    ID_TIPO_DOCU_ETIQUETA,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    MAX_LENGTH,
    MIN_LENGTH,
    OBLIGATORIO,
    ORDEN,
    REGEX,
    TIPO_DOCUMENTO_ID,
    LABEL_KEY,
    TIPO_DATO,
    FRACTIONS
) VALUES (
    DB_DOCUMENTAL.SEQ_ADMI_TIPO_DOCU_ETIQUETA.NEXTVAL,
    SYSDATE,
    'kbaque',
    '127.0.0.1',
    30,
    1,
    'N',
    4,
    '^([0-9|a-zA-Z]+[-| ])*[0-9|a-zA-Z]+$',
    (
        SELECT
            ID_TIPO_DOCUMENTO
        FROM
            DB_DOCUMENTAL.ADMI_TIPO_DOCUMENTO
        WHERE
            DESCRIPCION = 'Orden de Servicio'
    ),
    'contractNumber',
    'Text',
    0
);
--Configuración Token
INSERT INTO DB_TOKENSECURITY.WEB_SERVICE VALUES (
    DB_TOKENSECURITY.SEQ_WEB_SERVICE.NEXTVAL,
    'SoporteWSController',
    'procesarAction',
    1,
    'ACTIVO',
    (
        SELECT
            ID_APPLICATION
        FROM
            DB_TOKENSECURITY.APPLICATION
        WHERE
            NAME = 'Portal-SD'
    )
);
COMMIT;
/