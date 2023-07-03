INSERT INTO DB_COMERCIAL.INFO_OFICINA_GRUPO 
VALUES (DB_COMERCIAL.SEQ_INFO_OFICINA_GRUPO.NEXTVAL, 18, 75, 'MEGADATOS - VIRTUAL', 'Av. Rodrigo de Chávez Parque Empresarial Colón Edif. ColonCorp Torre 6 Locales comerciales 4 y 5',
        '2265050', '444', NULL, '593', 'N', 'N', 'Activo', 'epin', sysdate, '127.0.0.1', 'N', 'ECUATORIANO', NULL, NULL, NULL, NULL, '013', NULL, NULL);

INSERT INTO DB_COMERCIAL.ADMI_NUMERACION 
VALUES (DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL, '18', (SELECT ID_OFICINA
                                                         FROM DB_COMERCIAL.INFO_OFICINA_GRUPO
                                                         WHERE NOMBRE_OFICINA = 'MEGADATOS - VIRTUAL'),
        'Numeracion de Orden de trabajo Oficina Virtual', 'ORD', '001', '237', '1', sysdate, 'epin', NULL, NULL, 'info_orden_trabajo', 'Activo',
        NULL, 'N', NULL);

INSERT INTO DB_COMERCIAL.ADMI_NUMERACION 
VALUES (DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL, '18', (SELECT ID_OFICINA
                                                         FROM DB_COMERCIAL.INFO_OFICINA_GRUPO
                                                         WHERE NOMBRE_OFICINA = 'MEGADATOS - VIRTUAL'),
        'Numeracion de Contrato Oficina Virtual', 'CON', '001', '237', '1', sysdate, 'epin', NULL, NULL, 'info_contrato', 'Activo',
        NULL, 'N', NULL);    
    
INSERT INTO DB_COMERCIAL.ADMI_NUMERACION 
VALUES (DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL, '18', (SELECT ID_OFICINA
                                                         FROM DB_COMERCIAL.INFO_OFICINA_GRUPO
                                                         WHERE NOMBRE_OFICINA = 'MEGADATOS - VIRTUAL'),
        'Numeracion de Contrato Adendum Oficina Virtual', 'CONA', '001', '237', '1', sysdate, 'epin', NULL, NULL, 'info_adendum', 'Activo',
        NULL, 'N', NULL);    

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'OFICINA VIRTUAL', 'OFICINAS VIRTUALES PARA NUMERACION', 'COMERCIAL', 'CONTRATO_DIGITAL', 'Activo', 'epin', sysdate, '127.0.0.1', NULL, NULL, NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
values(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO
                                                   FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                   WHERE NOMBRE_PARAMETRO = 'OFICINA VIRTUAL'),
'OFICINA MEGADATOS', 237, NULL, NULL, NULL, 'Activo', 'epin', sysdate, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL, NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'ESTADO SERVICIO PARA ADENDUM', 'ESTADO DE LOS SERVICIOS QUE SE ENVIAN PARA ADENDUM', 'COMERCIAL', 'CONTRATO_DIGITAL', 'Activo', 'epin', sysdate, '127.0.0.1', NULL, NULL, NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
values(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO
                                                   FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                   WHERE NOMBRE_PARAMETRO = 'ESTADO SERVICIO PARA ADENDUM'),
'ESTADOS ACTIVO', '|PrePlanificada|Planificada|Activo|', 'Activo', NULL, NULL, 'Activo', 'epin', sysdate, '127.0.0.1', NULL, NULL, NULL, NULL, '18', NULL, NULL, NULL);


COMMIT;

