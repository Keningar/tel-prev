DELETE FROM DB_FIRMAELECT.ADM_EMP_PLANT_CERT 
WHERE PLANTILLA_ID = (SELECT PLA.ID_EMPRESA_PLANTILLA
                     FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA PLA
                     WHERE PLA.COD_PLANTILLA = 'adendumMegaDatos');
                     
DELETE FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA 
WHERE COD_PLANTILLA = 'adendumMegaDatos';

DELETE FROM DB_FIRMAELECT.ADM_EMPRESA_PARAMETRO
WHERE CLAVE = 'adendumMegaDatos';

DELETE FROM db_comercial.admi_numeracion WHERE CODIGO='CONA';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
where PARAMETRO_ID = (SELECT ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                      WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
where PARAMETRO_ID = (SELECT ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                      WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL';




COMMIT;
/