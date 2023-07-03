
    /*
    * Se realiza la inserción de parámetros para la creación de proyectos de TelcoS+ a TelcoCRM
    * @author Kevin Baque <kbaque@telconet.ec>
    * @version 1.0 03-07-2020
    */
    --Creamos la tarea y proceso para la creación de centro de costos.
    INSERT INTO db_soporte.admi_proceso (
        id_proceso,
        nombre_proceso,
        descripcion_proceso,
        estado,
        usr_creacion,
        usr_ult_mod,
        fe_creacion,
        fe_ult_mod,
        visible
    ) VALUES (
        db_soporte.seq_admi_proceso.nextval,
        'TAREAS CONTABILIDAD - GESTIÓN PROYECTO',
        'TAREAS CONTABILIDAD - GESTIÓN PROYECTO',
        'Activo',
        'kbaque',
        'kbaque',
        sysdate,
        sysdate,
        'SI'
    );

    INSERT INTO db_soporte.admi_proceso_empresa (
        id_proceso_empresa,
        proceso_id,
        empresa_cod,
        estado,
        usr_creacion,
        fe_creacion
    ) VALUES (
        db_soporte.seq_admi_proceso_empresa.nextval,
        (SELECT id_proceso FROM db_soporte.admi_proceso WHERE nombre_proceso='TAREAS CONTABILIDAD - GESTIÓN PROYECTO' AND estado='Activo'),
        '10',
        'Activo',
        'kbaque',
        sysdate
    );

    INSERT into db_soporte.admi_tarea(
        id_tarea,
        proceso_id,
        nombre_tarea,
        descripcion_tarea,
        estado,
        usr_creacion,
        usr_ult_mod,
        fe_creacion,
        fe_ult_mod
    ) VALUES (
        db_soporte.seq_admi_tarea.nextval,
        (select id_proceso from db_soporte.admi_proceso where nombre_proceso='TAREAS CONTABILIDAD - GESTIÓN PROYECTO' and estado='Activo'),
        'CREACION DE CTA. CONTABLE PARA PROYECTO',
        'CREACION DE CTA. CONTABLE PARA PROYECTO',
        'Activo',
        'kbaque',
        'kbaque',
        sysdate,
        sysdate
    );

    --Creamos la característica ID_PROYECTO.
    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO)
    VALUES
    (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ID_PROYECTO',
    'S',
    'Activo',
    sysdate,
    'kbaque',
    'TECNICA'
    );

    --Agregamos a todos los productos de TN la característica.
    INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
    SELECT
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ID_PRODUCTO,
    (select ID_CARACTERISTICA from DB_COMERCIAL.admi_caracteristica where DESCRIPCION_CARACTERISTICA='ID_PROYECTO'),
    SYSDATE,
    'kbaque',
    'Activo',
    'NO'
    FROM (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE ESTADO='Activo' and EMPRESA_COD=10);

    --Creamos la característica NOMBRE_PROYECTO.
    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO)
    VALUES
    (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'NOMBRE_PROYECTO',
    'S',
    'Activo',
    sysdate,
    'kbaque',
    'TECNICA'
    );

    --Agregamos a todos los productos de TN la característica.
    INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
    SELECT
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ID_PRODUCTO,
    (select ID_CARACTERISTICA from DB_COMERCIAL.admi_caracteristica where DESCRIPCION_CARACTERISTICA='NOMBRE_PROYECTO'),
    SYSDATE,
    'kbaque',
    'Activo',
    'NO'
    FROM (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE ESTADO='Activo' and EMPRESA_COD=10);

    --Creamos la característica TIPO_PROYECTO.
    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO)
    VALUES
    (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'TIPO_PROYECTO',
    'S',
    'Activo',
    sysdate,
    'kbaque',
    'TECNICA'
    );

    --Agregamos a todos los productos de TN la característica.
    INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,USR_CREACION,ESTADO,VISIBLE_COMERCIAL)
    SELECT
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ID_PRODUCTO,
    (select ID_CARACTERISTICA from DB_COMERCIAL.admi_caracteristica where DESCRIPCION_CARACTERISTICA='TIPO_PROYECTO'),
    SYSDATE,
    'kbaque',
    'Activo',
    'NO'
    FROM (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE ESTADO='Activo' and EMPRESA_COD=10);

    --Ingreso de una nueva solicitud.
    INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD (
        ID_TIPO_SOLICITUD,
        DESCRIPCION_SOLICITUD,
        FE_CREACION,
        USR_CREACION,
        FE_ULT_MOD,
        USR_ULT_MOD,
        ESTADO,
        TAREA_ID,
        ITEM_MENU_ID,
        PROCESO_ID)
    VALUES (
        DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
        'SOLICITUD DE PROYECTO',
        SYSDATE,
        'kbaque',
        SYSDATE,
        'kbaque',
        'Activo',
        NULL,
        NULL,
        NULL);

    --Ingreso de la cabecera para los parámetros necesarios para la interacción con TelcoCRM.
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
        'PARAMETROS_TELCOCRM',
        'PARAMETROS AUXILIARES QUE INTERACTUAN CON TELCOCRM',
        'COMERCIAL',
        'Activo',
        'kbaque',
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
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PMO',
        'cralarcon',
        'Alarcon Arreaga Carmen Rosa',
        'cralarcon@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PMO',
        'acaizapanta',
        'Caizapanta Tamayo Antonio Alfonso',
        'acaizapanta@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PMO',
        'jcalle',
        'Calle Regalado Jaime Andres',
        'jcalle@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PMO',
        'jjara',
        'Jara Alvear Joseline Alexia',
        'jjara@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PMO',
        'lmorocho',
        'Morocho Bastidas Lilia Salome',
        'lmorocho@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PMO',
        'asegarra',
        'Segarra Zambrano Ana Lucia',
        'asegarra@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PMO',
        'mvaldez',
        'Valdez Mora Miriam Cecilia',
        'mvaldez@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'cdalmeida',
        'Almeida Chicaiza Christian David',
        'cdalmeida@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'sburgos',
        'Burgos Rosado Silvia Mariela',
        'sburgos@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'ocastilloq',
        'Castillo Quimis Oscar Xavier',
        'ocastilloq@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'jlcastillot',
        'Castillo Tipantasi Jose Luis',
        'jlcastillot@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'vcastro',
        'Castro Villagomez Veronica Natalia',
        'vcastro@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'hchiluisa',
        'Chiluisa Lopez Haydi Guissela',
        'hchiluisa@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'vcruz',
        'Cruz Cargua Veronica Lucia',
        'vcruz@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'kcorrea',
        'Correa Ullon Karen Gabriela',
        'kcorrea@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'lgaibor',
        'Gaibor Noboa Luis Alberto',
        'lgaibor@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'dagallardo',
        'Gallardo Reascos Diana Alejandra',
        'dagallardo@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'ggavilanez',
        'Gavilanez Perrazo Gabriela Fernanda',
        'ggavilanez@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'clhidalgo',
        'Hidalgo Jimenez Cristina Lisbeth',
        'clhidalgo@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'rpuruncajas',
        'Puruncajas Manzano Ricardo Fernando',
        'rpuruncajas@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'jruiz',
        'Ruiz Andrade Jessica Kathiuska',
        'jruiz@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'rsalavarria',
        'Salavarria Mendoza Roger Eugenio',
        'rsalavarria@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'wsalazar',
        'Salazar Salazar Wendy Priscilla',
        'wsalazar@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_PROYECTO',
        'PYL',
        'ansanchez',
        'Sanchez Zambrano Angela Rosa',
        'ansanchez@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    --Ingresos de listado de usuarios permitidos para el envío de la notificación de proyecto reasignado.
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_NOTIFICACION_REASIGNACION',
        'PYL',
        'asegarra',
        'Proyectos',
        'PMO',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_NOTIFICACION_REASIGNACION',
        'PMO',
        'vcastro',
        'PLANIFICACION Y LOGISTICA',
        'PYL',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    --Ingresos de listado de usuarios permitidos para el envío de la notificación.
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_NOTIFICACION',
        'PMO',
        'vjimbo',
        'Bodega',
        'N',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );
        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_NOTIFICACION',
        'PMO',
        'sblacio',
        'Compras Locales',
        'N',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );
        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'LISTADO_USUARIOS_NOTIFICACION',
        'PMO',
        'fvalarezo',
        'Contabilidad',
        'S',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    --Ingresos de los estados permitidos para las solicitudes de proyecto.
        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'ESTADOS_PROYECTOS',
        'Pendiente',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );
        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'ESTADOS_PROYECTOS',
        'Aprobado',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );
        INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'ESTADOS_PROYECTOS',
        'Rechazado',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'TAREA_PROYECTO',
        'PMO',
        'TAREAS PMO - GESTIÓN PROYECTO',
        'Gerenciar Proyecto-Asignación Proyecto',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'TAREA_PROYECTO',
        'PYL',
        'TAREAS DE PYL - GESTION PROYECTOS',
        'IMPLEMENTACION PROYECTOS',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'TAREA_PROYECTO_CONTABILIDAD',
        'PMO',
        'TAREAS CONTABILIDAD - GESTIÓN PROYECTO',
        'CREACION DE CTA. CONTABLE PARA PROYECTO',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
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
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'MOTIVO_REASIGNACION_PROYECTO',
        'PYL',
        'Proyecto requiere PMO',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
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
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'MOTIVO_REASIGNACION_PROYECTO',
        'PMO',
        'Proyecto requiere PYL',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    --Ingresamos los motivos de rechazo
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
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'MOTIVO_RECHAZO_PROYECTO',
        'PMO',
        'Proyecto no asignado por G.T.N.',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
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
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'MOTIVO_RECHAZO_PROYECTO',
        'PYL',
        'No aplica proyecto',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
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
        (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'PARAMETROS_TELCOCRM' AND ESTADO      = 'Activo'),
        'MOTIVO_RECHAZO_PROYECTO',
        'PYL',
        'Información incorrecta',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO db_general.admi_parametro_det (
        id_parametro_det,
        parametro_id,
        descripcion,
        valor1,
        valor2,
        valor3,
        valor4,
        valor5,
        estado,
        usr_creacion,
        fe_creacion,
        ip_creacion,
        empresa_cod
    ) VALUES (
        db_general.seq_admi_parametro_det.nextval,
        (
            SELECT
                id_parametro
            FROM
                db_general.admi_parametro_cab
            WHERE
                nombre_parametro = 'GRUPO_ROLES_PERSONAL'
        ),
        'JEFE_DEPARTAMENTAL_PYL',
        'NO',
        '1',
        'JEFE_DEPARTAMENTAL_PYL',
        'ES_JEFE',
        'PYL',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );
    INSERT INTO db_general.admi_parametro_det (
        id_parametro_det,
        parametro_id,
        descripcion,
        valor1,
        valor2,
        valor3,
        valor4,
        valor5,
        estado,
        usr_creacion,
        fe_creacion,
        ip_creacion,
        empresa_cod
    ) VALUES (
        db_general.seq_admi_parametro_det.nextval,
        (
            SELECT
                id_parametro
            FROM
                db_general.admi_parametro_cab
            WHERE
                nombre_parametro = 'GRUPO_ROLES_PERSONAL'
        ),
        'JEFE_DEPARTAMENTAL_PMO',
        'NO',
        '1',
        'JEFE_DEPARTAMENTAL_PMO',
        'ES_JEFE',
        'PMO',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    --Ingreso de caracteristica Jefe departamental pyl y pmo
    INSERT INTO db_comercial.info_persona_empresa_rol_carac (
        id_persona_empresa_rol_caract,
        persona_empresa_rol_id,
        caracteristica_id,
        valor,
        fe_creacion,
        usr_creacion,
        ip_creacion,
        estado
    ) VALUES (
        db_comercial.seq_info_persona_emp_rol_carac.nextval,
        (
            SELECT
                iper.id_persona_rol
            FROM
                db_comercial.info_persona               ip
                JOIN db_comercial.info_persona_empresa_rol   iper ON iper.persona_id = ip.id_persona
                JOIN db_comercial.info_empresa_rol           ier ON ier.id_empresa_rol = iper.empresa_rol_id
                JOIN db_comercial.admi_rol                   arrol ON arrol.id_rol = ier.rol_id
            WHERE
                ip.login = 'vcastro'
                AND ip.estado = 'Activo'
                AND ier.estado IN (
                    'Activo',
                    'Modificado'
                )
                AND ier.empresa_cod = 10
                AND arrol.descripcion_rol = 'Jefe Departamental'
                AND iper.estado = 'Activo'
        ),
        (
            SELECT
                id_caracteristica
            FROM
                db_comercial.admi_caracteristica
            WHERE
                descripcion_caracteristica = 'CARGO_GRUPO_ROLES_PERSONAL'
                AND estado = 'Activo'
        ),
        (
            SELECT
                apd.id_parametro_det
            FROM
                db_general.admi_parametro_cab   apc
                JOIN db_general.admi_parametro_det   apd ON apd.parametro_id = apc.id_parametro
            WHERE
                apc.nombre_parametro = 'GRUPO_ROLES_PERSONAL'
                AND apc.modulo = 'COMERCIAL'
                AND apc.estado = 'Activo'
                AND apd.estado = 'Activo'
                AND apd.valor4 = 'ES_JEFE'
                AND apd.valor3 = 'JEFE_DEPARTAMENTAL_PYL'
        ),
        sysdate,
        'kbaque',
        '127.0.0.1',
        'Activo'
    );

    INSERT INTO db_comercial.info_persona_empresa_rol_carac (
        id_persona_empresa_rol_caract,
        persona_empresa_rol_id,
        caracteristica_id,
        valor,
        fe_creacion,
        usr_creacion,
        ip_creacion,
        estado
    ) VALUES (
        db_comercial.seq_info_persona_emp_rol_carac.nextval,
        (
            SELECT
                iper.id_persona_rol
            FROM
                db_comercial.info_persona               ip
                JOIN db_comercial.info_persona_empresa_rol   iper ON iper.persona_id = ip.id_persona
                JOIN db_comercial.info_empresa_rol           ier ON ier.id_empresa_rol = iper.empresa_rol_id
                JOIN db_comercial.admi_rol                   arrol ON arrol.id_rol = ier.rol_id
            WHERE
                ip.login = 'asegarra'
                AND ip.estado = 'Activo'
                AND ier.estado IN (
                    'Activo',
                    'Modificado'
                )
                AND ier.empresa_cod = 10
                AND arrol.descripcion_rol = 'Jefe Departamental'
                AND iper.estado = 'Activo'
        ),
        (
            SELECT
                id_caracteristica
            FROM
                db_comercial.admi_caracteristica
            WHERE
                descripcion_caracteristica = 'CARGO_GRUPO_ROLES_PERSONAL'
                AND estado = 'Activo'
        ),
        (
            SELECT
                apd.id_parametro_det
            FROM
                db_general.admi_parametro_cab   apc
                JOIN db_general.admi_parametro_det   apd ON apd.parametro_id = apc.id_parametro
            WHERE
                apc.nombre_parametro = 'GRUPO_ROLES_PERSONAL'
                AND apc.modulo = 'COMERCIAL'
                AND apc.estado = 'Activo'
                AND apd.estado = 'Activo'
                AND apd.valor4 = 'ES_JEFE'
                AND apd.valor3 = 'JEFE_DEPARTAMENTAL_PMO'
        ),
        sysdate,
        'kbaque',
        '127.0.0.1',
        'Activo'
    );

    --Ingreso de motivos.
    INSERT
    INTO DB_GENERAL.ADMI_MOTIVO
    (
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        CTA_CONTABLE
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        (SELECT
                id_relacion_sistema
            FROM
                db_seguridad.segu_relacion_sistema
            WHERE
                modulo_id = (
                    SELECT
                        id_modulo
                    FROM
                        db_seguridad.sist_modulo
                    WHERE
                        nombre_modulo = 'admiSolicitudProyecto'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Proyecto no asignado por por G.T.N.',
        'Activo',
        'kbaque',
        sysdate,
        'kbaque',
        sysdate,
        NULL
    );

    INSERT
    INTO DB_GENERAL.ADMI_MOTIVO
    (
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        CTA_CONTABLE
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        (SELECT
                id_relacion_sistema
            FROM
                db_seguridad.segu_relacion_sistema
            WHERE
                modulo_id = (
                    SELECT
                        id_modulo
                    FROM
                        db_seguridad.sist_modulo
                    WHERE
                        nombre_modulo = 'admiSolicitudProyecto'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Proyecto requiere PMO',
        'Activo',
        'kbaque',
        sysdate,
        'kbaque',
        sysdate,
        NULL
    );

    INSERT
    INTO DB_GENERAL.ADMI_MOTIVO
    (
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        CTA_CONTABLE
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        (SELECT
                id_relacion_sistema
            FROM
                db_seguridad.segu_relacion_sistema
            WHERE
                modulo_id = (
                    SELECT
                        id_modulo
                    FROM
                        db_seguridad.sist_modulo
                    WHERE
                        nombre_modulo = 'admiSolicitudProyecto'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Proyecto requiere PYL',
        'Activo',
        'kbaque',
        sysdate,
        'kbaque',
        sysdate,
        NULL
    );

    INSERT
    INTO DB_GENERAL.ADMI_MOTIVO
    (
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        CTA_CONTABLE
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        (SELECT
                id_relacion_sistema
            FROM
                db_seguridad.segu_relacion_sistema
            WHERE
                modulo_id = (
                    SELECT
                        id_modulo
                    FROM
                        db_seguridad.sist_modulo
                    WHERE
                        nombre_modulo = 'admiSolicitudProyecto'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'No aplica proyecto',
        'Activo',
        'kbaque',
        sysdate,
        'kbaque',
        sysdate,
        NULL
    );

    INSERT
    INTO DB_GENERAL.ADMI_MOTIVO
    (
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        CTA_CONTABLE
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        (SELECT
                id_relacion_sistema
            FROM
                db_seguridad.segu_relacion_sistema
            WHERE
                modulo_id = (
                    SELECT
                        id_modulo
                    FROM
                        db_seguridad.sist_modulo
                    WHERE
                        nombre_modulo = 'admiSolicitudProyecto'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Información incorrecta',
        'Activo',
        'kbaque',
        sysdate,
        'kbaque',
        sysdate,
        NULL
    );
    COMMIT;
    /