/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para parametrizar el filtro de los estados en promociones de frnaja horaria
 * @author Jessenia Piloso <jpiloso@telconet.ec>
 * @version 1.0
 * @since 15-09-2022
 */

DECLARE

BEGIN

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
VALUES (db_general.seq_admi_parametro_det.nextval,'1675','ESTADOS PERMITIDO PROMO FRANJA HORARIA','Activo',null,null,null,'Activo','jpiloso',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION) 
VALUES (db_general.seq_admi_parametro_det.nextval,'1675','ESTADOS PERMITIDO PROMO FRANJA HORARIA','Inactivo',null,null,null,'Activo','jpiloso',sysdate,'127.0.0.1',null,null,null,null,'18',null,null,null);

COMMIT;

END;

/