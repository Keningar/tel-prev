SET DEFINE OFF;

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID,
                                                       FE_CREACION, FE_ULT_MOD, USR_CREACION, USR_ULT_MOD, ESTADO,
                                                       VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.nextval, 1281, 942, SYSDATE , null, 'ppin', null,
        'Activo', 'NO');

COMMIT;
/