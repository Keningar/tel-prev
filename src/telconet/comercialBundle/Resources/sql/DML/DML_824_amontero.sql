
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION, EMPRESA_COD)
VALUES(
	   DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,609,'GERENTE_VENTAS','NO','1','GERENTE_VENTAS','ES_JEFE','Activo','amontero',sysdate,'127.0.0.1',10);
     

--PABLO GIRALDOR VALLEJO
UPDATE  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC SET ESTADO='Activo', 
VALOR=(SELECT ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE DESCRIPCION = 'GERENTE_VENTAS' AND VALOR1='NO' AND VALOR2='1' AND VALOR3='GERENTE_VENTAS'), 
FE_ULT_MOD=SYSDATE, USR_ULT_MOD='amontero' WHERE ID_PERSONA_EMPRESA_ROL_CARACT=160071;

COMMIT;