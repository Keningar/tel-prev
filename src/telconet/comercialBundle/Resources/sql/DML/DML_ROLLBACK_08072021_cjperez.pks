/* REVERSO DE LOS PARAMETROS PARA VALIDAR LOS ARCHIVOS DEL CONTRATO CLOUDFORM POR TIPO DE ARCHIVO */
DELETE FROM ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
   SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONTRATO_CLOUDFORM_TIPO_ARCHIVO'
);
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONTRATO_CLOUDFORM_TIPO_ARCHIVO';
COMMIT;
/