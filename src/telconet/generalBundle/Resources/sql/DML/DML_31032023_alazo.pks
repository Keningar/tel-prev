 /**
  * Documentación para Registro parametro de SOLICITUD REQUERIMIENTOS DE CLIENTES
  *  
  * @author Andre Lazo <alazo@telconet.ec>
  * @version 1.0 31-03-2023
  */

--SOLICITUD REQUERIMIENTOS DE CLIENTES
Insert into DB_GENERAL.ADMI_PARAMETRO_DET   
(ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,VALOR4,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD,VALOR5,EMPRESA_COD,VALOR6,VALOR7,OBSERVACION,VALOR8,VALOR9) 
values (
DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
(select id_parametro from  DB_GENERAL.Admi_Parametro_Cab where NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES'),
'Solicitud Requerimientos de clientes',
'SOLICITUD REQUERIMIENTOS DE CLIENTES',
null,null,
'Facturación Requerimientos de Clientes',
'Activo',
'atarreaga',
sysdate,
'127.0.0.1',
null,null,null,
'telcos_req_clientes',
'33',
'S',
null,null,null,null);

COMMIT;
/
