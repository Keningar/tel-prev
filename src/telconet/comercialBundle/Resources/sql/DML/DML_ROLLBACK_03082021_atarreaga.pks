/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 03-08-2021    
 * Se crea reverso de configuraciones para adulto mayor fase 3.
 */

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_CATEGORIA_PLAN_ADULTO_MAYOR' AND ESTADO = 'Activo';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR2=NULL, VALOR3=NULL, VALOR4=NULL,
                                         VALOR5=NULL, VALOR6=NULL,VALOR7=NULL, OBSERVACION=NULL
 WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM  DB_GENERAL.ADMI_PARAMETRO_CAB 
					  WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR')
	AND DESCRIPCION = 'MOTIVO_DESC_ADULTO_MAYOR'
	AND VALOR1      = 'Beneficio 3era Edad / Adulto Mayor';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR4=NULL, VALOR7=NULL, OBSERVACION=NULL
 WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM  DB_GENERAL.ADMI_PARAMETRO_CAB 
					  WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD')
	AND DESCRIPCION = 'MOTIVO_DESC_DISCAPACIDAD'
	AND VALOR1      = 'Cliente con Discapacidad';	

--SE ELIMINA DETALLES DE PARAMETROS
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
	WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
	                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	                        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
	                        AND ESTADO             = 'Activo')
          AND DESCRIPCION IN ('CATEGORIA_PLAN_ADULTO_MAYOR','APLICA_DESC_TIPO_PLAN_COMERCIAL',
		  					  'ESTADOS_CONTRATO','PORCENTAJE_DESC_RESOLUCION_072021_ADULTO_MAYOR');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
	WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
	                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	                        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
	                        AND ESTADO             = 'Activo')
          AND DESCRIPCION IN ('MOTIVO_DESC_ADULTO_MAYOR')
		  AND VALOR1 = '3era Edad Resolución 07-2021';					

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
	WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
	                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	                        WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
	                        AND ESTADO             = 'Activo')
          AND VALOR1 IN ('MENSAJE_VALIDACION_TIPO_CATEGORIA_PLAN','MENSAJE_VALIDACION_NOEXISTE_TIPO_CATEGORIA_PLAN');								

UPDATE DB_GENERAL.ADMI_MOTIVO SET ESTADO = 'Activo' WHERE NOMBRE_MOTIVO = 'Beneficio 3era Edad / Adulto Mayor';  

DELETE FROM DB_GENERAL.ADMI_MOTIVO WHERE NOMBRE_MOTIVO = '3era Edad Resolución 07-2021' AND ESTADO = 'Activo';  

COMMIT;
/