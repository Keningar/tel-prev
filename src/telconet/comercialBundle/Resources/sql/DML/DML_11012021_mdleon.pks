Insert Into DB_COMERCIAL.ADMI_TIPO_SOLICITUD
(ID_TIPO_SOLICITUD,DESCRIPCION_SOLICITUD,FE_CREACION,USR_CREACION,ESTADO)
VALUES
(DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
'SOLICITUD FACTURACION ACUMULADA',
SYSDATE,
'mdleon',
'Activo');

Insert Into DB_COMERCIAL.ADMI_CARACTERISTICA
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO,DETALLE_CARACTERISTICA)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'NUM_FACTURA_ACUMULADA',
'N',
'Activo',
SYSDATE,
'mdleon',
'COMERCIAL',
'Factura a cambiar entre Mrc y Nrc');

Insert Into DB_COMERCIAL.ADMI_CARACTERISTICA
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO,DETALLE_CARACTERISTICA)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'VAL_FACTURA_ACUMULADA',
'N',
'Activo',
SYSDATE,
'mdleon',
'COMERCIAL',
'Factura a cambiar entre Mrc y Nrc');

Insert Into DB_COMERCIAL.ADMI_CARACTERISTICA
(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,TIPO,DETALLE_CARACTERISTICA)
VALUES
(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
'VENDEDOR_FACTURA',
'N',
'Activo',
SYSDATE,
'mdleon',
'COMERCIAL',
'Datos del Vendedor de la Factura a cambiar entre Mrc y Nrc');

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
        FE_ULT_MOD
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
                        nombre_modulo = 'admiSolicitudFacturaAcu'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Facturas no emitidas en meses anteriores por no contar con Orden de Compra.',
        'Activo',
        'mdleon',
        sysdate,
        'mdleon',
        sysdate
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
        FE_ULT_MOD
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
                        nombre_modulo = 'admiSolicitudFacturaAcu'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Facturas no emitidas en meses anteriores por no contar con Orden de Registro.',
        'Activo',
        'mdleon',
        sysdate,
        'mdleon',
        sysdate
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
        FE_ULT_MOD
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
                        nombre_modulo = 'admiSolicitudFacturaAcu'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Facturas no emitidas en meses anteriores por no contar con presupuesto aprobado.',
        'Activo',
        'mdleon',
        sysdate,
        'mdleon',
        sysdate
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
        FE_ULT_MOD
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
                        nombre_modulo = 'admiSolicitudFacturaAcu'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Facturas emitidas anticipadamente por pedido del cliente para pre-pagar meses.',
        'Activo',
        'mdleon',
        sysdate,
        'mdleon',
        sysdate
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
        FE_ULT_MOD
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
                        nombre_modulo = 'admiSolicitudFacturaAcu'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Por error de ingreso.',
        'Activo',
        'mdleon',
        sysdate,
        'mdleon',
        sysdate
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
        FE_ULT_MOD
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
                        nombre_modulo = 'admiSolicitudFacturaAcu'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Por inconveniente en el sistema que no permite poner NRC y lo ponen en MRC.',
        'Activo',
        'mdleon',
        sysdate,
        'mdleon',
        sysdate
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
        FE_ULT_MOD
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
                        nombre_modulo = 'admiSolicitudFacturaAcu'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'Traslados que se reflejan como MRC que en realidad son NRC.',
        'Activo',
        'mdleon',
        sysdate,
        'mdleon',
        sysdate
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
        FE_ULT_MOD
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
                        nombre_modulo = 'admiSolicitudFacturaAcu'
                )
                AND accion_id = (
                    SELECT
                        id_accion
                    FROM
                        db_seguridad.sist_accion
                    WHERE
                        nombre_accion = 'index'
                )),
        'El sistema no permite los pagos mensuales, ejemplo consumos (Netvoice o Setel)',
        'Activo',
        'mdleon',
        sysdate,
        'mdleon',
        sysdate
    );

insert into DB_GENERAL.ADMI_PARAMETRO_CAB 
 (ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD)
 VALUES
 (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
 'CAMBIO_FACTURA_COMISION',
 'CAMBIAR FACTURA ENTRE MRC Y NRC',
 'COMERCIAL',
 'Activo',
 'mdleon',
 sysdate,
 '127.0.0.1',
 'mdleon',
 sysdate,
 '127.0.0.1');
 
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
 (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
 VALUES
 (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
 (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='CAMBIO_FACTURA_COMISION' AND MODULO='COMERCIAL'),
 'ESTADO_FACTURA',
 'Pendiente SubGerente',
 'Activo',
 'mdleon',
 sysdate,
 '127.0.0.1',
 10);
 
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
 (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
 VALUES
 (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
 (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='CAMBIO_FACTURA_COMISION' AND MODULO='COMERCIAL'),
 'ESTADO_FACTURA',
 'Pendiente Gerente',
 'Activo',
 'mdleon',
 sysdate,
 '127.0.0.1',
 10);
 
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
 (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
 VALUES
 (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
 (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='CAMBIO_FACTURA_COMISION' AND MODULO='COMERCIAL'),
 'ESTADO_FACTURA',
 'Aprobada',
 'Activo',
 'mdleon',
 sysdate,
 '127.0.0.1',
 10);
 
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
 (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
 VALUES
 (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
 (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='CAMBIO_FACTURA_COMISION' AND MODULO='COMERCIAL'),
 'ESTADO_FACTURA',
 'Rechazado',
 'Activo',
 'mdleon',
 sysdate,
 '127.0.0.1',
 10);

COMMIT;

/