/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear directorio en NFS donde se almacenarán fotos de Empleados
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0
 * @since 15-09-2022
 */

declare

ln_codigo_app number;
Ln_ID_PARAMETRO number;
begin

  select max(codigo_app) max_codigo_app
  into ln_codigo_app
  from db_general.admi_gestion_directorios s;

  insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
  estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
  values(db_general.seq_admi_gestion_directorios.nextval,ln_codigo_app+1,1,'Naf','593','TN','Empleados','Fotos',
  'Activo',sysdate,null,'psvelez',null);

  Ln_ID_PARAMETRO:= DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;

  insert into DB_GENERAL.ADMI_PARAMETRO_CAB(ID_PARAMETRO,
              NOMBRE_PARAMETRO,
              DESCRIPCION,
              ESTADO,
              USR_CREACION,
              FE_CREACION,
              IP_CREACION)
  VALUES(Ln_ID_PARAMETRO,
        'URL_MICROSERVICIO',
        'Url de los diferentes microservicios ',
        'Activo',
        'psvelez',
        SYSDATE,
        '127.0.0.1');

  insert into DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
            PARAMETRO_ID,
            DESCRIPCION,
            VALOR1,
            ESTADO,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION)
  VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
         Ln_ID_PARAMETRO,
         'URL_NFS_GUARDAR_ARCHIVO',
         'https://microservicios.telconet.ec/nfs/procesar',
         'Activo',
         'psvelez',
         sysdate,
         '127.0.0.1');

  insert into DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
            PARAMETRO_ID,
            DESCRIPCION,
            VALOR1,
            VALOR2,
            ESTADO,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION)
  VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
         Ln_ID_PARAMETRO,
         'PATH_URL_PUBLICA',
         'nfs7',
         'https://images.telconet.net/gp76cwdo/s8744534/lsa83sai/4b4snqrb/ryt9tz3u/28pleibo/',
         'Activo',
         'psvelez',
         sysdate,
         '127.0.0.1');

insert into DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
            PARAMETRO_ID,
            DESCRIPCION,
            VALOR1,
            VALOR2,
            ESTADO,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION)
  VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
         Ln_ID_PARAMETRO,
         'PATH_URL_PUBLICA',
         'nfs8',
         'https://images.telconet.net/4w0khmb2/s8744534/hg4zza2k/ei3tzdhu/srm9za20/mu9gxbp7/',
         'Activo',
         'psvelez',
         sysdate,
         '127.0.0.1');

insert into DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
            PARAMETRO_ID,
            DESCRIPCION,
            VALOR1,
            VALOR2,
            ESTADO,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION)
  VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
         Ln_ID_PARAMETRO,
         'PATH_URL_PUBLICA',
         'nfs9',
         'https://images.telconet.net/ndtmof7a/mvznccad/hcboltdi/llipzfjs/avcwuwxp/xafyhypk/',
         'Activo',
         'psvelez',
         sysdate,
         '127.0.0.1');

insert into DB_GENERAL.ADMI_PARAMETRO_DET(ID_PARAMETRO_DET,
            PARAMETRO_ID,
            DESCRIPCION,
            VALOR1,
            VALOR2,
            ESTADO,
            USR_CREACION,
            FE_CREACION,
            IP_CREACION)
  VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
         Ln_ID_PARAMETRO,
         'PATH_URL_PUBLICA',
         'nfs10',
         'https://images.telconet.net/jxrxswwf/bepnxbzy/bemifzrz/ycjmphhc/rhmggetu/ajnrlmoz/',
         'Activo',
         'psvelez',
         sysdate,
         '127.0.0.1');
  commit;

end;

/
