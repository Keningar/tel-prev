/**
* INSERT DE PARAMETROS PARA VALIDACIÓN POR MODELO OLT AL 
* 
* @author Alberto Arias <farias@telconet.ec>
* @version 1.0 05-12-2022
* 
*/

/* PARAMETROS */
/* CREACIÓN DEL PARÁMETRO CAB  - ADMINISTRACION_PUERTO_TARJETA_OLT*/

-- INSERT CABECERA
INSERT INTO db_general.ADMI_PARAMETRO_CAB 
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD) 
VALUES (db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,'ADMINISTRACION_PUERTO_TARJETA_OLT',
        'CANTIDAD DE TARJETAS Y PUERTOS EN LA ADMINISTRACION DE TARJETAS OLT',
        'TECNICO','ADMINISTRACION_PUERTO_TARJETA_OLT','Activo','farias',SYSDATE,'127.0.0.1',NULL,NULL,NULL);
        

/* DB_GENERAL.ADMI_PARAMETRO_DET */

--1
--INSERT - MODELO MA5608T
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5608T','0/',
    '8',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5608T','1/',
    '16',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');
    
    
--INSERT - MODELO C610
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C610','0/',
    '8',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C610','1/',
    '16',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

--INSERT - MODELO C320
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C320','0/',
    '8',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C320','1/',
    '16',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

    
--2
--INSERT - MODELO MA5800-X7
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5800-X7','1/',
    '8',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5800-X7','2/',
    '16',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5800-X7','3/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5800-X7','4/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5800-X7','5/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');
    
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5800-X7','6/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');
    
    
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','MA5800-X7','7/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');    
    
    


--3
--INSERT - MODELO C650
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C650','1/',
    '8',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');    
  
INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C650','2/',
    '16',NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');    

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C650','3/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');  
 
 INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C650','4/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');  
 
 
 INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C650','7/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');  
 
 INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C650','8/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');  
 
 INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ADMINISTRACION_PUERTO_TARJETA_OLT'),
    'valor1: Nombre del modelo, valor2: tarjetas, valor3: cantidad de puertos','C650','9/',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1','18');  
 
 
--Se habilita la tecnologia ZTE para producto Small Business
UPDATE DB_GENERAL.ADMI_PARAMETRO_CAB 
SET estado='Activo'
WHERE nombre_parametro='ISB_TECNOLOGIAS_NO_PERMITIDAS';

INSERT INTO db_general.admi_parametro_det 
(id_parametro_det,parametro_id,descripcion,valor1,valor2,valor3,valor4,valor5,valor6,valor7,estado,usr_creacion,fe_creacion,ip_creacion,empresa_cod) 
VALUES (db_general.seq_admi_parametro_det.nextval,(SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                  WHERE NOMBRE_PARAMETRO = 'ISB_TECNOLOGIAS_NO_PERMITIDAS'),
    'ISB_TECNOLOGIAS_NO_PERMITIDAS','TECNOLOGIAS','MA5800-X7',
    NULL,NULL,NULL,NULL,NULL,'Activo','farias',SYSDATE,'127.0.0.1',NULL);  

 
    
COMMIT;
/
