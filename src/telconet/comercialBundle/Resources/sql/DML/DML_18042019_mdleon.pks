--Se crea la caracteristica; NIVEL ESCALABILIDAD para los tipos de contaco de Seguridad Escalable
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(id_caracteristica,descripcion_caracteristica,tipo_ingreso,estado,fe_creacion,usr_creacion,tipo)
VALUES
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'NIVEL ESCALABILIDAD',
    'S',
    'Activo',
    sysdate,
    'mdleon',
    'COMERCIAL'
);

--Se crea la caracteristica; HORARIO ESCALABILIDAD para los tipos de contaco de Seguridad Escalable
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(id_caracteristica,descripcion_caracteristica,tipo_ingreso,estado,fe_creacion,usr_creacion,tipo)
VALUES
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'HORARIO ESCALABILIDAD',
    'S',
    'Activo',
    sysdate,
    'mdleon',
    'COMERCIAL'
);


--Se registra un nuevo tipo de contacto que manejara el nivel de escalabilidad
INSERT INTO DB_GENERAL.ADMI_ROL
(ID_ROL,TIPO_ROL_ID,DESCRIPCION_ROL,ESTADO,USR_CREACION,FE_CREACION,ES_JEFE,PERMITE_ASIGNACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_ROL.NEXTVAL,
    5,
    'Contacto Seguridad Escalable',
    'Activo',
    'mdleon',
    sysdate,
    'N',
    'N'
);

--Se registra el tipo de contacto ingresado para las diferentes empresas
INSERT INTO DB_COMERCIAL.INFO_EMPRESA_ROL
(id_empresa_rol,empresa_cod,rol_id,estado,usr_creacion,fe_creacion,ip_creacion)
VALUES
(
    DB_COMERCIAL.SEQ_INFO_EMPRESA_ROL.NEXTVAL,
    10,
    (SELECT ID_ROL FROM DB_GENERAL.ADMI_ROL WHERE DESCRIPCION_ROL='Contacto Seguridad Escalable'),
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

--Se registra el tipo de contacto ingresado para las diferentes empresas
INSERT INTO DB_COMERCIAL.INFO_EMPRESA_ROL
(id_empresa_rol,empresa_cod,rol_id,estado,usr_creacion,fe_creacion,ip_creacion)
VALUES
(
    DB_COMERCIAL.SEQ_INFO_EMPRESA_ROL.NEXTVAL,
    26,
    (SELECT ID_ROL FROM DB_GENERAL.ADMI_ROL WHERE DESCRIPCION_ROL='Contacto Seguridad Escalable'),
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);


--Se registra una nueva forma de contacto; Telegram
INSERT INTO DB_COMERCIAL.ADMI_FORMA_CONTACTO
(ID_FORMA_CONTACTO,DESCRIPCION_FORMA_CONTACTO,FE_CREACION,USR_CREACION,ESTADO,CODIGO)
VALUES
(
    DB_COMERCIAL.SEQ_ADMI_FORMA_CONTACTO.NEXTVAL,
    'Telegram',
    sysdate,
    'mdleon',
    'Activo',
    'TGRAM'
);

--Registramos los tipos de Escalabilidad con los que podra contar un cliente
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PERMITE_ESCALABILIDAD',
    'RETORNA LOS DIFERENTES TIPOS DE ESCALABILIDAD QUE POSEE UN USUARIO',
    'COMERCIAL',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PERMITE_ESCALABILIDAD'),
    'PRIMER ENLACE DE COMUNICACION',
    'PRIMER NIVEL',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PERMITE_ESCALABILIDAD'),
    'SEGUNDO ENLACE DE COMUNICACION',
    'SEGUNDO NIVEL',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PERMITE_ESCALABILIDAD'),
    'TERCER ENLACE DE COMUNICACION',
    'TERCER NIVEL',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

--Registramos los tipos de Horarios con los que podra contar un cliente
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'HORARIO_CONTACTOS',
    'RETORNA LOS DIFERENTES TIPOS DE HORARIOS QUE SE ENCUENTRAN REGISTRADOS PARA LA COMUNICACION CON EL USUARIO',
    'COMERCIAL',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='HORARIO_CONTACTOS'),
    'ATENCION A TODA HORA',
    'HORARIO 24X7',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='HORARIO_CONTACTOS'),
    'HORARIO OFICINA',
    'HORARIO 8x5',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

--Ingresamos los tipos de productos que estaran sujetos a la validacion de Escalabilidad
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PRODUCTOS CON FILTRO ESCALABLES',
    'INDICA LOS PRODUCTOS QUE SERAN VALIDADOS QUE CUENTEN CON EL NIVEL DE ESCALABILDAD',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY COMPLEMENTARY',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY CONSULTING',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY CSOC',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY DATABASE',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY DDoS PROTECTION',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY EMAIL PROTECTION',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY EQUIPMENT',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY NG FIREWALL',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY WEB SECURITY',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SECURITY WEBSITE PROTECTION',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD ELECTRONICA   CONTROL DE ACCESOS',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD ELECTRONICA   VIDEO VIGILANCIA',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD ELECTRONICA CABLEADO ESTRUCTURADO',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD ELECTRONICA SISTEMAS CONTRA INCENDIO',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD FISICA CUSTODIA',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD FISICA ESCOLTA ARMADA',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD FISICA GUARDIANIA',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD FISICA MONITOREO',
    'Activo',
    'mdleon',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO='PRODUCTOS CON FILTRO ESCALABLES'),
    'PRODUCTO DE FILTRO ESCALABLES',
    'SEGURIDAD FISICA TRANSPORTE DE VALORES',
    'Activo',
    'mdleon',
     sysdate,
    '127.0.0.1'
);

commit;

/