
--INSERT NUEVO PERFIL MÓVIL
INSERT INTO DB_SEGURIDAD.sist_perfil 
VALUES (DB_SEGURIDAD.SEQ_SIST_PERFIL.NEXTVAL , 
        'TMO Fiscalizar', 
        'Activo', 
        'wvera', 
        sysdate,
        null,
        null);

COMMIT;
