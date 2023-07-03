
/**
 * Documentación INSERT DE PARÁMETROS PARA SABER SI ES PROYECTO Y A QUIEN SE DEBE REPORTAR
 * INSERT de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * @author David León <mdleon@telconet.ec>
 * @version 1.0 12-06-2022
 */
   
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'VALIDA_VALOR_PROYECTO_NAF',
        'PARAMETROS PARA VALIDAR POR EL VALOR SI ES UN PROYECTO O NO',
        'COMERCIAL',
        'Activo',
        'mdleon',
        SYSDATE,
        '127.0.0.1'
    );
    --Ingresos de listado de usuarios que se les podrá asignar los proyectos PMO.
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
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'VALIDA_VALOR_PROYECTO_NAF' AND ESTADO      = 'Activo'),
        'VALIDA SI ES PROYECTO Y SI APLICA PARA CONTABILIZAR',
        '10000',
        '20000',
        'Activo',
        'mdleon',
        SYSDATE,
        '127.0.0.1',
        10,
        'VALOR1 = APLICA PROYECTO, VALOR2 = ES CONTABLE'
    );

--Ingreso de la cabecera para los parámetros necesarios para generar tarea a contabilidad.
   
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'DATOS_TAREA_CONTADOR',
        'PARAMETROS PARA GENERAR LA TAREA AL CONTADOR',
        'COMERCIAL',
        'Activo',
        'mdleon',
        SYSDATE,
        '127.0.0.1'
    );
    --Ingresos de listado de usuarios que se les podrá asignar los proyectos PMO.
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
	VALOR3,
        VALOR4,
	VALOR5,
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
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'DATOS_TAREA_CONTADOR' AND ESTADO      = 'Activo'),
        'DATOS PARA CREAR TAREA AL AREA CONTABLE',
        'fvalarezo',
        'CONTABILIDAD',
	'TAREAS DE CONTABILIDAD - GESTION DE PROYECTOS CRM',
	'CREAR PROYECTO',
	'Se crea Tarea por Proyecto',
        'Activo',
        'mdleon',
        SYSDATE,
        '127.0.0.1',
        10,
        'VALOR1 = APLICA PROYECTO, VALOR2 = ES CONTABLE'
    );

COMMIT;

/