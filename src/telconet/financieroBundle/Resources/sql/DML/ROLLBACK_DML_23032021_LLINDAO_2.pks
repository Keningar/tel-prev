--
DELETE DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET APCD
WHERE EXISTS ( SELECT NULL
               FROM ADMI_PLANTILLA_CONTABLE_CAB APCC
               WHERE APCC.ID_PLANTILLA_CONTABLE_CAB = APCD.PLANTILLA_CONTABLE_CAB_ID
               AND APCC.EMPRESA_COD = '10'
               AND APCC.DESCRIPCION = 'ANTICIPO AUTOMATICO');
--
--
DELETE DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB APCC
WHERE APCC.EMPRESA_COD = '10'
AND APCC.DESCRIPCION = 'ANTICIPO AUTOMATICO';
--
--
/

