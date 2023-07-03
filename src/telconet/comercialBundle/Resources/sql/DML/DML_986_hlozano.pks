INSERT 
INTO DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO) values 
    (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'NETLIFECLOUD',
        'O',
        'Activo',
        SYSDATE,
        'hlozano',
        SYSDATE,
        'hlozano',
        'COMERCIAL'
    );

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA,PRODUCTO_ID,CARACTERISTICA_ID,FE_CREACION,FE_ULT_MOD,USR_CREACION,USR_ULT_MOD,ESTADO,VISIBLE_COMERCIAL) values 
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO ='NetlifeCloud'),
        (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA='NETLIFECLOUD'),
        SYSDATE,
        null,
        'hlozano',
        null,
        'Activo',
        'NO'
    );

COMMIT;


