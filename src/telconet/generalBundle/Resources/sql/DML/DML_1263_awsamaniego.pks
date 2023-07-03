SET SERVEROUTPUT ON;

DECLARE
    Ln_SeqParametroCab   NUMBER := 0;
BEGIN
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'REQUEST_FIND_LDAP',
        'Parametros para preparar request de busqueda en LDAP, de la sincronizacion de empleados',
        'NAF47',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Valores para el request',
        'http://test-apps.telconet.ec/ldap/api/v1/directory',
        'GET',
        ' HTTP/1.1',
        'Content-Type',
        'application/json',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'REQUEST_CREATE_LDAP',
        'Parametros para prepara request de creacion en LDAP, de la sincronizacion de empleados',
        'NAF47',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Valores para el request',
        'http://test-apps.telconet.ec/ldap/api/v1/directory',
        'POST',
        ' HTTP/1.1',
        'Content-Type',
        'application/json',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;
    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'REQUEST_UPDATE_LDAP',
        'Parametros para prepara request de actualizacion en LDAP, de la sincronizacion de empleados',
        'NAF47',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Valores para el request',
        'http://test-apps.telconet.ec/ldap/api/v1/directory',
        'PUT',
        ' HTTP/1.1',
        'Content-Type',
        'application/json',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;
    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'REQUEST_DELETE_LDAP',
        'Parametros para prepara request de eliminacion en LDAP, de la sincronizacion de empleados',
        'NAF47',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Valores para el request',
        'http://test-apps.telconet.ec/ldap/api/v1/directory',
        'DELETE',
        ' HTTP/1.1',
        'Content-Type',
        'application/json',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;
    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'LDAP_CLASS_ATTR_ENTITY',
        'Atributos necesarios para entidad LDAP',
        'NAF47',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Atributos',
        '/home/',
        '/bin/bash',
        '{0} telcoPerson, inetOrgPerson, organizationalPerson, person, top, posixAccount',
        'Usuario Interno',
        'Usuarios',
        '{0} organizationalUnit, top',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;
    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'MAIL_SYNC_EMPLEADO',
        'Configuracion para envio de correo',
        'NAF47',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        EMPRESA_COD,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Atributos',
        'notificaciones_telcos@telconet.ec',
        'Ingreso Empleado',
        'Actualización Empleado',
        'Salida de Empleado',
        'Error en sincronización de Empleado',
        10,
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;
    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'ESTADOS_PERSONA_EMPRESA_ROL',
        'Contiene los estados que no se deben considerar en INFO_PERSONA_EMPRESA_ROL',
        'COMERCIAL',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Estados que no se debe considerar',
        'Cancelado',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Estados que no se debe considerar',
        'Eliminado',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Estados que no se debe considerar',
        'Inactivo',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'PERFILES_DEFAULT_SINCRONIZACION_EMPLEADOS',
        'Contiene perfiles que deben ser configurados por default al sincronizar un empleado',
        'SEGURIDAD',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Perfil',
        'Perfil Administracion Contrasena',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Perfil',
        'Ver Tarjeta Olt',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );
    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'TIPO_ROL_PERSONA_EMPRESA_ROL',
        'Contiene los tipos de rol a considerar al buscar en INFO_PERSONA_EMPRESA_ROL',
        'COMERCIAL',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Tipo rol a considerar',
        'Empleado',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Tipo rol a considerar',
        'Pasantes',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'LOGGER_APP_SINC_EMPL_NAF_TELCOS',
        'Permite escribir log en el proceso de sincronizacion de empleados ',
        'NAF47_TNET',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'S => Escribe , N => No escribe',
        'S',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'STRING_PASSWD',
        'Contiene configuracion para generar password',
        'NAF47_TNET',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Valor1 => caracteres a usar, Valor2 => Length de valor1, Valor3 => Length de password',
        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#$%&()=?¿_-*+,.|',
        '79',
        '10',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;
    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'USER_APP_SINC_EMPL_NAF_TELCOS',
        'Usuario que escribe en el proceso de sincronizacion de empleados ',
        'NAF47_TNET',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'USER',
        'ssosync',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'ZIMBRA_API_SOAP',
        'Parametros para los request CRUD hacia zimbra',
        'NAF47',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
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
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'zimbraPassword',
        DB_GENERAL.GNCK_STRING.ENCRYPT_STR('admin'),
        DB_GENERAL.GNCK_STRING.ENCRYPT_STR('zimbra1.'),
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
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
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'AuthRequest',
        '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:zimbra" xmlns:urn1="urn:zimbraAdmin"><soapenv:Body><urn1:AuthRequest name="${name}" password="${password}"></urn1:AuthRequest></soapenv:Body></soapenv:Envelope>'
        ,
        'http://test-apps.telconet.ec/service/admin/soap/',
        'POST',
        ' HTTP/1.1',
        'Content-Type',
        'text/xml;charset=UTF-8;',
        '"urn:zimbraAdmin/Auth"',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
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
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'GetAccountRequest',
        '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:zimbra" xmlns:urn1="urn:zimbraAdmin"><soapenv:Header><urn:context><urn:authToken>${token}</urn:authToken></urn:context></soapenv:Header><soapenv:Body><urn1:GetAccountRequest><urn1:account by="name">${name}</urn1:account></urn1:GetAccountRequest></soapenv:Body></soapenv:Envelope>'
        ,
        'http://test-apps.telconet.ec/service/admin/soap/',
        'POST',
        ' HTTP/1.1',
        'Content-Type',
        'text/xml;charset=UTF-8;',
        '"urn:zimbraAdmin/GetAccount"',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
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
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'CreateAccountRequest',
        '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:zimbra" xmlns:urn1="urn:zimbraAdmin"><soapenv:Header><urn:context><urn:authToken>${token}</urn:authToken></urn:context></soapenv:Header><soapenv:Body><urn1:CreateAccountRequest name="${name}" password="${password}">${urn}</urn1:CreateAccountRequest></soapenv:Body></soapenv:Envelope>'
        ,
        'http://test-apps.telconet.ec/service/admin/soap/',
        'POST',
        ' HTTP/1.1',
        'Content-Type',
        'text/xml;charset=UTF-8;',
        '"urn:zimbraAdmin/CreateAccount"',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
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
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'DeleteAccountRequest',
        '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:zimbra" xmlns:urn1="urn:zimbraAdmin"><soapenv:Header><urn:context><urn:authToken>${token}</urn:authToken></urn:context></soapenv:Header><soapenv:Body><urn1:DeleteAccountRequest id="${id}"/></soapenv:Body></soapenv:Envelope>'
        ,
        'http://test-apps.telconet.ec/service/admin/soap/',
        'POST',
        ' HTTP/1.1',
        'Content-Type',
        'text/xml;charset=UTF-8;',
        '"urn:zimbraAdmin/DeleteAccount"',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
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
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'ModifyAccountRequest',
        '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:zimbra" xmlns:urn1="urn:zimbraAdmin"><soapenv:Header><urn:context><urn:authToken>${token}</urn:authToken></urn:context></soapenv:Header><soapenv:Body><urn1:ModifyAccountRequest id="${id}">${urn}</urn1:ModifyAccountRequest></soapenv:Body></soapenv:Envelope>'
        ,
        'http://test-apps.telconet.ec/service/admin/soap/',
        'POST',
        ' HTTP/1.1',
        'Content-Type',
        'text/xml;charset=UTF-8;',
        ' "urn:zimbraAdmin/ModifyAccount"',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'USER_ZIMBRA',
        'Usuario que escribe en el proceso mirror zimbra ',
        'NAF47_TNET',
        'ZIMBRA',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'USER',
        'zimbra',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'LOGGER_ZIMBRA',
        'Permite escribir log en el mirror zimbra ',
        'NAF47_TNET',
        'ZIMBRA',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'S => Escribe , N => No escribe',
        'S',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;


    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'USE_ZIMBRA',
        'Permite usar api zimbra ',
        'NAF47_TNET',
        'ZIMBRA',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'Empresa para usar zimbra',
        '10',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'NEW_SYNC',
        'Permite usar api zimbra ',
        'NAF47_TNET',
        'P_SINCRONIZACION_CON_TELCOS',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        'S para ir por el proceso, cualquier otro valor lo cancela',
        'S',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;

    Ln_SeqParametroCab := 0;
    Ln_SeqParametroCab := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
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
        Ln_SeqParametroCab,
        'CREATE_UPDATE_URN_ZIMBRA',
        'Contenido urn para crear o actualizar objeto zimbra',
        'NAF47_TNET',
        'ZIMBRA',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        Ln_SeqParametroCab,
        '<urn> usado para crear o actualizar objeto zimbra',
        '<urn1:a n="zimbraAuthLdapExternalDn">${dn}</urn1:a><urn1:a n="displayName">${displayName}</urn1:a><urn1:a n="givenName">${givenName}</urn1:a><urn1:a n="sn">${sn}</urn1:a><urn1:a n="zimbraPrefTimeZoneId">America/Bogota</urn1:a>',
        'Activo',
        'awsamaniego',
        SYSDATE,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS')
    );

    COMMIT;


    DBMS_OUTPUT.PUT_LINE('Commited!!');
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        DBMS_OUTPUT.PUT_LINE('Error: ' || SQLERRM);
END;
/
