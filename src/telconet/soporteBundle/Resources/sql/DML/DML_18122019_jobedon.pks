/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Se configura rutas del FILESERVER para Telconet y Megadatos
 * Ruta base: PAIS/COD_EMP/AÑO/MES/DIA/APP/MODULO/SUBMODULO
 *
 * @author José Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 18-12-2019 - Versión Inicial.
 */

INSERT 
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'RUTAS_ARCHIVOS','Configuracion de rutas para FILESERVER','SOPORTE','FILESERVER','Activo','jobedon',SYSDATE,
    '127.0.0.1', null, null, null);
    
INSERT 
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,'MODULOS_APP','Configuracion Modulos por APlicacion para uso de FileServer','SOPORTE','FILESERVER','Activo','jobedon',SYSDATE,
    '127.0.0.1', null, null, null);

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'RUTAS_ARCHIVOS'),
    'SUFIJO_RUTA_ARCHIVO','TELCOS','SOPORTE','CASOS','TELCOS/SOPORTE/CASOS/','Activo','jobedon',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'RUTAS_ARCHIVOS'),
    'SUFIJO_RUTA_ARCHIVO','TELCOS','SOPORTE','TAREAS','TELCOS/SOPORTE/TAREAS/','Activo','jobedon',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'RUTAS_ARCHIVOS'),
    'PREFIJO_RUTA_ARCHIVO_TN',593,'TN',NULL,'593/TN/','Activo','jobedon',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'RUTAS_ARCHIVOS'),
    'PREFIJO_RUTA_ARCHIVO_MD',593,'MD',NULL,'593/MD/','Activo','jobedon',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'MODULOS_APP'),
    'TELCOS_SOPORTE','TN','TELCOS','SOPORTE',NULL,'Activo','jobedon',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,(SELECT A.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'MODULOS_APP'),
    'EXTENSIONES_RESTRINGIDAS','exe,bash,msi,com,bat,cmd,vb,vbs,wsf,scf,scr,pif,rar,jsp,html,php,php5,pht,phtml,shtml,asa,cer,asax,swf,xap',NULL,NULL,NULL,'Activo','jobedon',SYSDATE,
    '127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

COMMIT;

/