--

DELETE FROM  DB_COMUNICACION.INFO_DOCUMENTO
WHERE TIPO_DOCUMENTO_GENERAL_ID IN (SELECT ID_TIPO_DOCUMENTO FROM db_general.admi_tipo_documento_general  WHERE codigo_tipo_documento='TCSP');

DELETE FROM db_general.admi_tipo_documento_general atdg
WHERE ATDG.CODIGO_TIPO_DOCUMENTO = 'TCSP';
--



-----Terminos y Condiciones

DELETE FROM DB_FIRMAELECT.ADM_EMP_PLANT_CERT t WHERE t.PLANTILLA_ID=  (SELECT PLA.ID_EMPRESA_PLANTILLA
         FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA PLA
         WHERE PLA.COD_PLANTILLA = 'terminosCondicionesMegadatos');

DELETE FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.COD_PLANTILLA='terminosCondicionesMegadatos';


DELETE FROM DB_FIRMAELECT.ADM_EMPRESA_PARAMETRO
WHERE CLAVE = 'terminosCondicionesMegadatos';


COMMIT;
/