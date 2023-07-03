
    /*
    * Se realiza la inserción de parámetros para registar la url de TelcoCRM.
    * @author Kevin Baque <kbaque@telconet.ec>
    * @version 1.0 29-12-2022
    */
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
        'URL_TELCOCRM',
        'http://telcos-ws-ext-lb.telconet.ec/TelcoCRM/custom/service/v4_1_custom/rest.php',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );
    COMMIT;
    /