/*
* Se crean script de reverso para las caracteristica de Holsing.
* @author David León <mdleon@telconet.ec>
* @version 1.0 30-10-2020
*/
DELETE FROM  DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO='HOLDING DE EMPRESAS';

DELETE FROM COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA='HOLDING EMPRESARIAL';

COMMIT;

/