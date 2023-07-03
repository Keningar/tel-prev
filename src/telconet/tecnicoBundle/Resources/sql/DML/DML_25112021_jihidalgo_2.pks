/**
 * DEBE EJECUTARSE EN DB_GENERAL.
 * Parametrizaciones de Motivos para el proceso Corte - Inaudit Posible Abusador
 * DESCRIPCION = NOMBRE_MOTIVO
 * VALOR1 = ID_MOTIVO
 * VALOR2 = PREFIJO EMPRESA 'MD'
 * @author Javier Hidalgo Fernández <jihidalgo@telconet.ec>
 * @version 1.0 25-11-2021 - Versión Inicial.
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'MOTIVOS_INAUDITAR_CLIENTE', 'MOTIVOS DE CORTE - INAUDIT A POSIBLE ABUSADOR','TECNICO','INAUDITAR_CLIENTE','Activo','jihidalgo',SYSDATE,'127.0.0.1', NULL, NULL, NULL);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MOTIVOS_INAUDITAR_CLIENTE'), 'Incumplimiento del contrato (ISP-Cyber)', (SELECT ID_MOTIVO FROM DB_GENERAL.ADMI_MOTIVO WHERE NOMBRE_MOTIVO = 'Incumplimiento del contrato (ISP-Cyber)'), 'MD', NULL, NULL, 'Activo', 'jihidalgo', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, NULL);

/**
 * DEBE EJECUTARSE EN DB_GENERAL.
 * Parametrizaciones de Perfiles con permisos para proceso de Reconexión de Cliente Posible Abusador
 * DESCRIPCION = NOMBRE_PERFIL
 * VALOR1 = ID_PERFIL
 * VALOR2 = PREFIJO EMPRESA 'MD'
 * @author Javier Hidalgo Fernández <jihidalgo@telconet.ec>
 * @version 1.0 25-11-2021 - Versión Inicial.
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'PERFILES_CONECTAR_POSIBLES_ABUSADORES', 'PERFILES QUE PUEDEN RECONECTAR CLIENTE POSIBLE ABUSADOR','TECNICO','CONECTAR_POSIBLE_ABUSADOR','Activo','jihidalgo',SYSDATE,'127.0.0.1', NULL, NULL, NULL);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PERFILES_CONECTAR_POSIBLES_ABUSADORES'), 'Md_Coordinador_Servicio_Cliente', (SELECT ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Md_Coordinador_Servicio_Cliente'), 'MD', NULL, NULL, 'Activo', 'jihidalgo', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, NULL);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PERFILES_CONECTAR_POSIBLES_ABUSADORES'), 'Md_Coordinador_Calidad', (SELECT ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Md_Coordinador_Calidad'), 'MD', NULL, NULL, 'Activo', 'jihidalgo', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, NULL);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PERFILES_CONECTAR_POSIBLES_ABUSADORES'), 'Md_Agente_Calidad', (SELECT ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Md_Agente_Calidad'), 'MD', NULL, NULL, 'Activo', 'jihidalgo', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, NULL);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PERFILES_CONECTAR_POSIBLES_ABUSADORES'), 'Md_Coordinador_IPCC', (SELECT ID_PERFIL FROM DB_SEGURIDAD.SIST_PERFIL WHERE NOMBRE_PERFIL = 'Md_Coordinador_IPCC'), 'MD', NULL, NULL, 'Activo', 'jihidalgo', SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, NULL);

COMMIT;
/