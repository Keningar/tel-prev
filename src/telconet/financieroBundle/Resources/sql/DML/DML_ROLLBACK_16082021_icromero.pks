/**
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0
 * @since 16-08-2021    
 * BORRA REGISTROS PARA LAS PROMOCIONES DE LAS FORMAS DE PAGO INGRESADAS EN ESTE PASE
 * TARJETAS MASTERCARD TITANIUM -TARJETAS UNION PAY BANKARD- COOPERATIVAS VARIAS
 */
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE USR_CREACION = 'icromero' AND PARAMETRO_ID =(select id_parametro from DB_GENERAL.admi_parametro_cab where NOMBRE_PARAMETRO = 'MAPEO DE PROMOCIONES MENSUAL' AND ESTADO ='Activo');
COMMIT;
