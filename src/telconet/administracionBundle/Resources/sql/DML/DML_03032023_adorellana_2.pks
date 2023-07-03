--INSERTS PARA AGREGAR PARAMETROS A LA TABLA ADMI_PARAMETRO_DET

SET SERVEROUTPUT ON;

DECLARE

    lv_nombreparam   VARCHAR2(30) := 'MENSAJES_ADMIN_NOTIF_PUSH';
    lv_estadoactivo  VARCHAR2(10) := 'Activo';
    lv_modulo        VARCHAR2(20) := 'ADMINISTRACION';
    
    CURSOR lc_getvaloresparamscab (cv_nombreparametro IN VARCHAR2) 
    IS
    SELECT
        cab.id_parametro
    FROM
        db_general.admi_parametro_cab cab
    WHERE
            cab.nombre_parametro = cv_nombreparametro
        AND cab.estado = lv_estadoactivo
        AND cab.modulo = lv_modulo;

    lr_datosparamcab lc_getvaloresparamscab%rowtype;
    
BEGIN

    OPEN lc_getvaloresparamscab(lv_nombreparam);
    FETCH lc_getvaloresparamscab INTO lr_datosparamcab;
    CLOSE lc_getvaloresparamscab;
    dbms_output.put_line( lr_datosparamcab.id_parametro);
   
    INSERT INTO db_general.admi_parametro_det (
        id_parametro_det,
        parametro_id,
        descripcion,
        valor1,
        valor2,
        valor3,
        valor4,
        estado,
        usr_creacion,
        fe_creacion,
        ip_creacion,
        usr_ult_mod,
        fe_ult_mod,
        ip_ult_mod,
        valor5,
        empresa_cod,
        valor6,
        valor7,
        observacion
    ) VALUES (
        db_general.seq_admi_parametro_det.nextval,
        lr_datosparamcab.id_parametro,
        'NOTI_PUSH_MENSAJE_ELIMINAR',
        'La notificación seleccionada fue eliminada exitosamente',
        NULL,
        NULL,
        NULL,
        'Activo',
        'adorellana',
        sysdate,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL,
        NULL,
        'Mensaje a enviar por pantalla cuando se elimina correctamente una notifificacion push'
    );

    INSERT INTO db_general.admi_parametro_det (
        id_parametro_det,
        parametro_id,
        descripcion,
        valor1,
        valor2,
        valor3,
        valor4,
        estado,
        usr_creacion,
        fe_creacion,
        ip_creacion,
        usr_ult_mod,
        fe_ult_mod,
        ip_ult_mod,
        valor5,
        empresa_cod,
        valor6,
        valor7,
        observacion
    ) VALUES (
        db_general.seq_admi_parametro_det.nextval,
        lr_datosparamcab.id_parametro,
        'NOTI_PUSH_MENSAJE_CLONAR',
        'Notificación push clonada de forma exitosa, por favor proceda a su edición',
        NULL,
        NULL,
        NULL,
        'Activo',
        'adorellana',
        sysdate,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL,
        NULL,
        'Mensaje a enviar por pantalla cuando se clona correctamente una notifificacion push'
    );

    COMMIT;
END;
/